cd /home/ubuntu/projects/localio-live && php artisan tinker --execute='DB::statement(\
ALTER
TABLE
category_translations
ADD
COLUMN
comparison_slug
VARCHAR
255
NULL
AFTER
slug\);'
