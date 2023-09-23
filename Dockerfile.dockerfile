services:
  web:
    image: php:7.4-apache
    container_name: php_apache_server
    volumes:
      - ./html:/var/www/html
    ports:
      - "8000:80"
    depends_on:
      - db
    environment:
      DATABASE_HOST: db
      DATABASE_USER: user
      DATABASE_PASSWORD: password
      DATABASE_NAME: mydb

  db:
    image: mysql:5.7
    container_name: mysql_server
    volumes:
      - db_data:/var/lib/mysql
    environment:
      MYSQL_ROOT_PASSWORD: kali
      MYSQL_DATABASE: kali
      MYSQL_USER: kali
      MYSQL_PASSWORD: kali
    ports:
      - "3306:3306"

volumes:
  db_data: