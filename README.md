# Appetite Link

### Laravel APIs + Vue Dashboard
Url: https://app.appetite.link

This web app was built following standard Laravel + Vue scaffolding with Inertia.js
https://laravel.com/docs/9.x/frontend

```
composer install
npm install
```

Build the assets

```
npm prod
```

### SQL / Database Dump
Can be downloaded here:
https://drive.google.com/file/d/15Z4P33sYHbSQyNmydZd7S1QC6kqCcIGe/view?usp=sharing

### FTP Details (Prod)
Can be found in the .vscode folder

### phpmyadmin
https://four.appetitecreative.com/RRupbzsqM0
the database details can be found on the .env file in the server (after s/ftp connection)

### Setup

This is quite a old setup and we only have a server

The ftp details are connected to the production server
Typically you can build the assets (npm run build) and simply upload any files over there after local testing

### Notes

- Need to setup local domains to access as app.appetitelink:8080

127.0.0.1    go.appetitelink
127.0.0.1    app.appetitelink
127.0.0.1    query.appetitelink
127.0.0.1    share.appetitelink