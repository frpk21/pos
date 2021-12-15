from django.db import models

class Link(models.Model):
    
    key = models.SlugField( 
        verbose_name="Nombre clave", max_length=100)
    name = models.CharField( 
        verbose_name="Red social", max_length=200)
    url = models.URLField(
        verbose_name="Enlace", max_length=200, null=True, blank=True)

    class Meta:
        verbose_name = "enlace"
        verbose_name_plural = "enlaces"
        ordering = ['name']

    def __str__(self):
        return self.name