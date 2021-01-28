set :repo_url, 'git@github.com:RealT-Community/RealT-API.git'

## Docker Config
namespace :docker do
    desc "Custom RealT jobs for containers/Symfony config"
    task :realt do
        on roles(:staging) do
            within release_path do
                # Restart containers on Staging
                execute "runuser", "-l", "realt", "-c", "'cd /home/realt/docker/api/preprod && ./current/.circleci/continous_deployment.sh'"
#                 execute "pwd"
#                 execute "ls", "-lah"
#                 execute "docker-compose", "build"
#                 execute "docker-compose", "-f", "docker-compose.preprod.yml", "up", "-d", "--force"
#                 execute "docker-compose", "-f", "docker-compose.preprod.yml", "exec", "-T", "symfony-preprod", "php", "bin/console", "doctrine:migrations:migrate"
            end
        end

        on roles(:production) do
            within release_path do
                # Restart containers on Production
                execute "docker-compose", "build"
                execute "docker-compose", "-f", "docker-compose.yml", "up", "-d", "--force"
                execute "docker-compose", "-f", "docker-compose.yml", "exec", "-T", "symfony", "php", "bin/console", "doctrine:migrations:migrate"
            end
        end
    end
end

namespace :deploy do
    desc 'Starting deployement'
#    after :publishing, "symfony:migrate"
    after :publishing, "docker:realt"
end