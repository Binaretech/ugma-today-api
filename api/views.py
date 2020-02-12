from rest_framework import views, viewsets
from rest_framework.decorators import api_view
from rest_framework.response import Response
from .serializers import UserSerializer, PostSerializer, ProfileSerializer
from rest_framework.authtoken.models import Token
from rest_framework.authtoken.views import ObtainAuthToken
from django.db import transaction
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
    }, status=201)


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


class PostViewSet(viewsets.ViewSet):
    def index(self, request):
        return PageNumberPagination(PostSerializer(Post.objects.all(), many=True).data)

    def store(self, request):
        post = PostSerializer(data=request.data)
        if not post.is_valid():
            return Response(post.errors)
        post.user = request.user
        post.save()
        return Response(post.data)
