from django.urls import include, path
from fact_digital import views
from django.conf.urls import url
from django.contrib.auth import views as auth_views
from django.contrib import admin
from django.conf import settings
from django.conf.urls.static import static

#conf_link = 'http://inraionline.com/fact_digital/status/'
urlpatterns = [
    path('status', views.StatusView, name='status'),
]