from rest_framework.urls import url
from .views.register import register, AuthViewSet 

urlpatterns = [
    url('login/', AuthViewSet.as_view()),
    url('register/', register)
]