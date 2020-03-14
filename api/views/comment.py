from rest_framework import permissions, status
from ..serializers import CommentSerializer
from rest_framework import views, viewsets
from rest_framework.pagination import PageNumberPagination
from rest_framework.decorators import permission_classes
from ..models import Comment, Post
from rest_framework.response import Response


class CommentViewSet(viewsets.ViewSet, PageNumberPagination):
    permission_classes = [permissions.IsAuthenticated]

    def index(self, request, post_id):
        try:
            post = Post.objects.get(id=post_id)
            return self.get_paginated_response(
                self.paginate_queryset(CommentSerializer(Comment.object.filter(post=post), many=True), request))
        except Post.DoesNotExist:
            return Response({'error': True, 'message': 'Publicacion no encontrada.'},
                            status.HTTP_404_NOT_FOUND)

    def show(self, request, id):
        try:
            comment = Comment.objects.get(id=id)
            return Response(CommentSerializer(comment).data)
        except Comment.DoesNotExist:
            return Response({
                'error': True,
                'message': 'Comentario no encontrado'
            })

    def store(self, request, post_id):
        try:
            post = Post.objects.get(id=post_id)
            comment = CommentSerializer(data=request.data)

            if not comment.is_valid():
                return Response(comment.errors)

            comment.save(post=post, user=request.user)
            return Response({'message': 'Comentario creado con éxito.'}, status.HTTP_201_CREATED)

        except Post.DoesNotExist:
            return Response({'error': True, 'message': 'Publicacion no encontrada.'},
                            status.HTTP_404_NOT_FOUND)
        except:
            return Response({'error': True, 'message': 'Error al guardar comentario'},
                            status.HTTP_500_INTERNAL_SERVER_ERROR)

    def destroy(self, request, id):
        try:
            comment = Comment.objects.get(id=id, user=request.user)
            comment.delete()
            return Response({'message': 'Comentario eliminado con éxito.'}, status.HTTP_200_OK)
        except Comment.DoesNotExist:
            return Response({'error': True, 'message': 'Comentario no encontrado.'}, status.HTTP_404_NOT_FOUND)
        except Exception as e:
            return Response({'error': True, 'message': 'Ha ocurrido un error.'}, status.HTTP_200_OK)
