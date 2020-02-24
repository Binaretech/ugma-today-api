from rest_framework import serializers
from .models import User, Profile, Post, Comment
from rest_framework.pagination import PageNumberPagination
from django.db import transaction


class ProfileSerializer(serializers.ModelSerializer):
    class Meta:
        model = Profile
        exclude = ['user']


class UserSerializer(serializers.ModelSerializer):
    profile = ProfileSerializer(many=False)
    password = serializers.CharField(write_only=True)

    class Meta:
        model = User
        fields = ['id', 'username', 'profile', 'password']
        read_only_fields = ['profile']

    def create(self, validated_data):
        with transaction.atomic():
            user = User.objects.create(username=validated_data['username'])
            user.set_password(validated_data['password'])
            user.save()
            Profile.objects.create(user=user, **validated_data['profile'])

        return user


class CommentSerializer(serializers.ModelSerializer):

    user = serializers.SerializerMethodField()

    class Meta:
        model = Comment
        fields = ['id', 'content', 'user']
        read_only_fields = ['user']

    def get_user(self, comment):
        return UserSerializer(User.objects.get(id=comment.user_id)).data


class PostSerializer(serializers.ModelSerializer):
    user = UserSerializer(many=False, read_only=True)
    comments = serializers.SerializerMethodField()

    class Meta:
        model = Post
        fields = ['id', 'comments', 'user', 'content']
        read_only_fields = ['user', 'comments']
        depth = 1

    def get_comments(self, post):
        paginator = PageNumberPagination()
        pagination = paginator.paginate_queryset(
            Comment.objects.filter(post=post), self.context['request'])
        return paginator.get_paginated_response(CommentSerializer(pagination, many=True, read_only=True).data).data
