from rest_framework.decorators import api_view
from rest_framework.response import Response
from ..serializers.user import UserSerializer
from rest_framework.authtoken.models import Token

@api_view(['POST'])
def register(request):
    user = UserSerializer(data=request.data)
    if not user.is_valid():
        return Response(user.errors)
    
    user.save()
    token = Token.objects.create(user=user.instance)
    return Response({
        'token': token.key,
        'user': user.data,
    })

