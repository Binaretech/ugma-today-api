from rest_framework import viewsets, views
from rest_framework.response import Response
from ..serializers import UserSerializer


class UserViewSet(views.APIView):
    def get(self, request):
        return Response(UserSerializer(instance=request.user).data)
