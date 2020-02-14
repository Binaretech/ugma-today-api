from rest_framework.urls import url
from .views import UserViewSet, PostViewSet, Authentication, register

urlpatterns = [
    url('login/', Authentication.as_view()),
    url('register/', register),
    url('user/', UserViewSet.as_view()),
    url('post/', PostViewSet.as_view({'post': 'store', 'get': 'index'})),
    url('post/<int:id>',
        PostViewSet.as_view({'get': 'show', 'delete': 'destroy'}))
]
