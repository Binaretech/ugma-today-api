from rest_framework import views, viewsets
from rest_framework.decorators import api_view
from rest_framework.response import Response
from .serializers import UserSerializer, PostSerializer, ProfileSerializer, CommentSerializer
from rest_framework.authtoken.models import Token
from rest_framework.authtoken.views import ObtainAuthToken
from django.db import transaction
from rest_framework import status
from .models import Post, Comment
from rest_framework.pagination import PageNumberPagination
from rest_framework.decorators import permission_classes
from .permissions import IsOwnerOrReadOnly
from rest_framework import permissions


@api_view(['POST'])
def register(request):
    user = UserSerializer(data=request.data)
    if not user.is_valid():
        return Response(user.errors, status=400)
    user.save()
    token = Token.objects.create(user=user.instance)
    return Response({
        'token': token.key,
        'user': user.data,
    }, status.HTTP_201_CREATED)


class Authentication(ObtainAuthToken):
    def post(self, request, *args, **kwargs):
        serializer = self.serializer_class(
            data=request.data, context={'request': request})

        serializer.is_valid(raise_exception=True)
        user = serializer.validated_data['user']
        token, _ = Token.objects.get_or_create(user=user)
        return Response({
            'token': token.key,
            'user': UserSerializer(instance=user).data
        })


class UserViewSet(views.APIView):
    permission_classes = (permissions.IsAuthenticated)

    def get(self, request):
        return Response(UserSerializer(instance=request.user).data)


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
        try:
            post = PostSerializer(data=request.data)
            if not post.is_valid():
                return Response(post.errors)
            post.save(user=request.user)
            return Response(post.data, status.HTTP_201_CREATED)
        except:
            return Response({'error': True, 'message': 'Error al guardar publicación'},
                            status.HTTP_500_INTERNAL_SERVER_ERROR)

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
