# Pronto OAuth Example Application

## Setup Development Environment using Docker
- Install Docker https://docs.docker.com/install/
- Run `docker-compose up`
  - This will build the docker image if it does not exist
  - This will also run the migration scripts.

- To tear it all down just hit `ctrl-c`

If you want to rebuild a docker image due to any changes made locally you can rebuild it using the following command

```
docker-compose build
```

- See what is running `docker ps`  (in a new terminal)
```
yourLaptop:oauth-example username$ docker ps
CONTAINER ID        IMAGE                COMMAND                  CREATED              STATUS                        PORTS                               NAMES
1f4a4a143995        oauth-example_web    "docker-php-entrypoi…"   About a minute ago   Up About a minute             0.0.0.0:8000->80/tcp                oauth-example_web_1
8acc3c0769ed        mysql/mysql-server   "/entrypoint.sh mysq…"   About a minute ago   Up About a minute (healthy)   0.0.0.0:3306->3306/tcp, 33060/tcp   oauth-example_mysql_1
a1e576503f13        redis                "docker-entrypoint.s…"   21 minutes ago       Up About a minute             6379/tcp                            oauth-example_redis_1
```
- Open a browser to http://localhost:8000/ and you should see a login/register page, which demonstrates that the app is running properly.
- Continue to the next step before using the application

## Update configuration for OAuth

- For this application to work with Pronto OAuth, it will need to be accessible from the internet, the steps below achieve this with ngrok (https://ngrok.com)
  -  After setting up ngrok, make sure to **access the application through the ngrok url (http://e98ee554263d.ngrok.io) not localhost!** otherwise there will be issues with HTTP sessions and the OAuth redirect processing!
- You'll need 3 parameters configured in `docker-compose.yml` for the entire OAuth flow to work:
  - `APP_URL`
  - `PRONTO_OAUTH_CLIENT_SECRET`
  - `PRONTO_OAUTH_CLIENT_ID`
- Optionally, you can set `PRONTO_URL` in `docker-compose.yml` if your organization is using a custom domain, such as `https://hogwarts.pronto.io` instead of `https://chat.pronto.io`
  - You may encounter session/cookie issues if this application is configured to use a different domain than the domain you use to log into the Pronto web application

Follow the steps below:

#### 1 - Use ngrok to allow access to your app from the internet, and to determine the OAuth redirect URL (based on `APP_URL`)
- Download ngrok: https://ngrok.com/
- Start up ngrok `./ngrok http 8000`
  - Identify the ngrok Forwarding URL displayed in the terminal
  - Set `APP_URL` in `docker-compose.yml` to what you see in ngrok. E.g. `APP_URL=http://e98ee554263d.ngrok.io`
```
ngrok by @inconshreveable                                                                                                                               (Ctrl+C to quit)
                                                                                                                                                                        
Session Status                online                                                                                                                                    
Session Expires               7 hours, 59 minutes                                                                                                                       
Version                       2.3.35                                                                                                                                    
Region                        United States (us)                                                                                                                        
Web Interface                 http://127.0.0.1:4040                                                                                                                     
Forwarding                    http://e98ee554263d.ngrok.io -> http://localhost:8000                                                                                     
Forwarding                    https://e98ee554263d.ngrok.io -> http://localhost:8000                                                                                    
                                                                                                                                                                        
Connections                   ttl     opn     rt1     rt5     p50     p90                                                                                               
                              0       0       0.00    0.00    0.00    0.00      
```

#### 2 - Generate an OAuth Client ID and Secret
- Log into Pronto (https://chat.pronto.io/) as an organization administrator. If you're reading this you should know how to do that.  
  - Settings -> Org Management -> Integrations
  - Create a new OAuth Client
    - Call it whatever you want
    - Using your ngrok URL (Step 1 above), add a redirect URL with the path `oauth/auth` such as `http://e98ee554263d.ngrok.io/oauth/auth`
      - This can be updated later as you change your ngrok URL
    - Click 'Create'
    - You will see a pop-up of sorts that gives you a secret key. This will only be displayed once. Copy this and put it into this application's `docker-compose.yml`: `PRONTO_OAUTH_CLIENT_SECRET=OEzNF1Emk1r********************z0zy9316k`
    - Now that the client is created you can see it in the list of OAuth Clients, you'll need to find the client ID and put that into this application's `docker-compose.yml` as well: `PRONTO_OAUTH_CLIENT_ID=16`
  - Pronto is expecting this application to contact it using the client you have created, and it also knows how to complete OAuth via the redirect URL you set.
- Restart this application using `docker-compose up` (above).
- This app is now configured to communicate through Pronto using your Pronto User account, and the account you set up in this application. 
  - <ins>To make sure the HTTP sessions and Pronto redirects work properly, make sure to use this app through the ngrok URL (e.g. http://e98ee554263d.ngrok.io), not localhost!</ins> 
  - This is a demo application, and it is connected is to a production environment.
    - This application is exposed to the internet through ngrok and only has basic security protections; it is not meant to run as a production service. Take precautions to secure your data!
    - Be careful with modifying data; don't modify your production data unless you truly intend to!
  
## Finally: Use the application

- Navigate to the application using the ngrok URL (e.g. http://e98ee554263d.ngrok.io)
- Create an account if you haven't done so already. A real email address / email verification is not required.
- Follow the on-screen instructions to connect to Pronto using OAuth 2.0.
  - Additional details here: https://support.pronto.io/en/articles/4165097-api-authorization 
