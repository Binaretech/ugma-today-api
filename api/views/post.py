from rest_framework import permissions, status
from rest_framework import views, viewsets
from rest_framework.pagination import PageNumberPagination
from rest_framework.decorators import permission_classes
from rest_framework.response import Response
from ..serializers import PostSerializer
from ..models import Post


class PostViewSet(viewsets.ViewSet, PageNumberPagination):
    permission_classes = [permissions.IsAuthenticated]

    def index(self, request):
        return self.get_paginated_response(
            self.paginate_queryset(PostSerializer(Post.objects.all(), many=True, context={'request': request}).data, request))

    def show(self, request, id):
        try:
            return Response(PostSerializer(instance=Post.objects.get(id=id), context={'request': request}).data)
        except Post.DoesNotExist:
            return Response({'error': True, 'message': 'Publicacion no encontrada'},
                            status.HTTP_404_NOT_FOUND)

    def store(self, request):
        # try:
        post = PostSerializer(data=request.data)
        if not post.is_valid():
            return Response(post.errors)
        post.save(user=request.user)
        return Response(post.data, status.HTTP_201_CREATED)
        # except:
        #     return Response({'error': True, 'message': 'Error al guardar publicación'},
        # status.HTTP_500_INTERNAL_SERVER_ERROR)

    def destroy(self, request, id):
        try:
            post = Post.objects.get(id=id, user=request.user)
            post.delete()
            return Response({'message': 'Publicación eliminada'})
        except Post.DoesNotExist:
            return Response({'error': True, 'message': 'Publicacion no encontrada.'},
                            status.HTTP_404_NOT_FOUND)
        except:
            return Response({'error': True, 'message': 'Ha ocurrido un error'},
                            status.HTTP_500_INTERNAL_SERVER_ERROR)
