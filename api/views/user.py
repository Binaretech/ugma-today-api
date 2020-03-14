from rest_framework import permissions
from ..serializers import UserSerializer
from ..models import User
from rest_framework.response import Response
from rest_framework import views, viewsets
from rest_framework.pagination import PageNumberPagination
from rest_framework.decorators import permission_classes


class UserViewSet(viewsets.ViewSet, PageNumberPagination):
    permission_classes = [permissions.IsAuthenticated]

    def index(self, request):
        return self.get_paginated_response(
            self.paginate_queryset(
                UserSerializer(User.objects.all(), many=True, context={'request': request}).data, request)
        )

    def show(self, request):
        return Response(UserSerializer(instance=request.user).data)
