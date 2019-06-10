# Install

1. Install Docker (`sudo apt-get install docker.io docker-compose`)
1. Enter the `docker` group (https://docs.docker.com/install/linux/linux-postinstall/ **Manage Docker as a non-root user** section)
1. Run `./from-scratch.sh` in project directory and follow instructions
1. Ensure that project is up by looking at `docker-compose ps` command response in parallel command line. All states must be `Up`. If they're not, please run `docker-compose up <container>` where `<container>` is the container which isn't Up. Put results to the new [Issue](TODO:link_to_new_issue)
1. When all is Up you can open your project instance in browser: http://play-a-game.localhost/ or via https://play-a-game.localhost/

# Regular start

```bash
./up
```

# Access

After first start and authorization you will be granted regular user access. To add admin role to your profile, execute this command:

```bash
docker-compose exec backend console user:role:add insideone admin
```

but put your steam profile nickname or steamId instead of `insideone`

# Update

You can update the site without wiping your user data:

```bash
./update
```

# More docs

* Backend
  * [Controllers](docs/backend/controllers.md)
  * [Validation](docs/backend/validation.md)
  * [Security](docs/backend/security.md)
  * [Xdebug](docs/backend/xdebug.md)
