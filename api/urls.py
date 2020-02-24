from django.urls import path
from rest_framework.urls import url
from .views import UserViewSet, PostViewSet, Authentication, register, CommentViewSet

urlpatterns = [
    url('login/', Authentication.as_view()),
    url('register/', register),
    url('user/', UserViewSet.as_view()),
    path('post/<int:id>/',
         PostViewSet.as_view({'get': 'show', 'delete': 'destroy'})),
    url('post/', PostViewSet.as_view({'post': 'store', 'get': 'index'})),
    path('post/<int:post_id>/comment/',
         CommentViewSet.as_view({'post': 'store', 'get': 'index'})),
    path('comment/<int:id>/',
         CommentViewSet.as_view({'get': 'show', 'delete': 'destroy'})),

]
