from rest_framework.authtoken import views
from rest_framework.urls import url
from .views.register import register
urlpatterns = [
    url('login/', views.obtain_auth_token),
    url('register/', register)
]