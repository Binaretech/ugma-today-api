from rest_framework.urls import url
from .views.auth import register, Authentication
from .views.userviewset import UserViewSet

urlpatterns = [
    url('login/', Authentication.as_view()),
    url('register/', register),
    url('user/', UserViewSet.as_view())
]
