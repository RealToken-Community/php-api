set :stage, :staging

server "preprod.realt.community", user: "root", port: 2222, roles: %w{all staging}

# The user that the server is running under (used for ACLs)
set :symfony_server_user, 'realt'
set :application, 'api.preprod.realt.ch'
set :branch, 'circleci-project-setup'

set :deploy_to, -> { "/home/realt/docker/api/preprod" }
set :repo_path, -> { "#{fetch(:deploy_to)}/repo" }

# Set .env files
#set :symfony_dotenv_file, './.env.staging' # (Uncomment if use cap in local)
#set :symfony_upload_dotenv_file_on_deploy, false

 set :linked_dirs, %w{logs}
set :linked_files, %w{.env .env.prod .env.testing}
set :keep_releases, 5

# Use local composer.phar
# SSHKit.config.command_map[:php] = '/usr/bin/php'
# SSHKit.config.command_map[:composer] = "/usr/bin/php #{release_path.join("composer.phar")}"

# For Debug
#set :composer_install_flags, '--no-interaction --optimize-autoloader'