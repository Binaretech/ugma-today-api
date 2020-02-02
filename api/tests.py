from django.test import TestCase
from django.urls import reverse
import random
import string


def randomString(stringLength=10):
    letters = string.ascii_lowercase
    return ''.join(random.choice(letters) for i in range(stringLength))


class AuthViewTests(TestCase):
    def test_register(self):
        response = self.client.post('/api/register/', data={
            'username': randomString(),
            'password': randomString(),
            'email': randomString()+'@'+randomString()+'.com'
        })

        self.assertEqual(response.status_code, 200)

    def test_login(self):
        username = randomString()
        password = randomString()
        response = self.client.post('/api/register/', data={
            'username': username,
            'password': password,
            'email': randomString()+'@'+randomString()+'.com'
        })

        self.assertEqual(response.status_code, 200)

        response = self.client.post('/api/login/', data={
            'username': username,
            'password': password,
            'email': randomString()+'@'+randomString()+'.com'
        })

        self.assertEqual(response.status_code, 200)
