from django.http import HttpResponse, HttpResponseRedirect
from generales.models import Profile
from .models import Link

def ctx_dict(request):
    ctx={}
    """
    #db_name
    #user_db
    #password_db 
    from pos import settings
    if request.user.is_authenticated == True:
        cliente_db = Profile.objects.get(user = request.user)
        database_id = 'db_'+str(cliente_db.user.id)
        newDatabase = {}
        newDatabase["id"] = 'default'
        newDatabase['ENGINE'] = 'django.db.backends.postgresql_psycopg2'
        newDatabase['NAME'] = cliente_db.db_name.strip()
        newDatabase['USER'] = cliente_db.user_db.strip()
        newDatabase['PASSWORD'] = cliente_db.password_db.strip()
        newDatabase['Host'] = 'localhost'
        newDatabase['PORT'] = '5432'
        print("wwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwww")
        print(newDatabase)
        settings.DATABASES[database_id] = newDatabase
    """
    links = Link.objects.all()
    for link in links:
        ctx[link.key] = link.url
    return ctx