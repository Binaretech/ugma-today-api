from rest_framework.authtoken import views
from rest_framework.urls import url
from .views.auth import AuthViewSet

urlpatterns = [
    url('login/', AuthViewSet.as_view({'post': 'login'})),
]