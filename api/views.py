from rest_framework import views, viewsets
from rest_framework.decorators import api_view
from rest_framework.response import Response
from .serializers import UserSerializer, PostSerializer, ProfileSerializer
from rest_framework.authtoken.models import Token
from rest_framework.authtoken.views import ObtainAuthToken
from django.db import transaction
from rest_framework import status
from .models import Post
from rest_framework.pagination import PageNumberPagination


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
    def get(self, request):
        return Response(UserSerializer(instance=request.user).data)


class PostViewSet(viewsets.ViewSet, PageNumberPagination):
    def index(self, request):
        return self.get_paginated_response(
            self.paginate_queryset(PostSerializer(Post.objects.all(), many=True).data, request))

    def show(self, request, id):
        try:
            post = Post.objects.get(id=id)
            return Response(PostSerializer(instance=post))
        except:
            return Response({'error': True, 'message': 'Publicacion no encontrada'},
                            status.HTTP_404_NOT_FOUND)

    def store(self, request):
        try:
            post = PostSerializer(data=request.data)
            if not post.is_valid():
                return Response(post.errors)
            post.user = request.user
            post.save()
            return Response(post.data, status.HTTP_201_CREATED)
        except:
            return Response({'error': True, 'message': 'Error al guardar publicación'},
                            status.HTTP_500_INTERNAL_SERVER_ERROR)

    def destroy(self, request, id):
        try:
            post = Post.objects.get(id=id)
            post.delete()
            return Response({'message': 'Publicación eliminada'})
        except:
            return Response({'error': True, 'message': 'Ha ocurrido un error'},
                            status.HTTP_500_INTERNAL_SERVER_ERROR)
