from rest_framework.decorators import api_view
from rest_framework.response import Response
from ..serializers import UserSerializer, ProfileSerializer
from rest_framework.authtoken.models import Token
from rest_framework.authtoken.views import ObtainAuthToken
from django.db import transaction


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
