from rest_framework.urls import url
from .views.auth import register, Authentication 

urlpatterns = [
    url('login/', Authentication.as_view()),
    url('register/', register)
]