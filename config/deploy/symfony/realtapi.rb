set :repo_url, 'git@github.com:RealT-Community/RealT-API.git'

## Default Symfony Config

namespace :symfony do
#     desc "Run Laravel Artisan migrate."
#     task :migrate do
#     	on roles(:all) do
#         	within release_path do
#                 symfony_console('doctrine:migrations:migrate', '--no-interaction')
#             end
#         end
#     end

    desc "Custom RealT jobs for containers/Symfony config"
    task :realt do
        on roles(:staging) do
            within release_path do
                # Restart containers on Staging
                execute "sudo", "docker-compose", "build"
                execute "sudo", "docker-compose", "-f", "docker-compose.preprod.yml", "up", "-d", "--force"
                execute "sudo", "docker-compose", "-f", "docker-compose.preprod.yml", "exec", "-T", "symfony-preprod", "php", "bin/console", "doctrine:migrations:migrate"

            end
        end

        on roles(:production) do
            within release_path do
                # Restart containers on Production
                execute "sudo", "docker-compose", "build"
                execute "sudo", "docker-compose", "-f", "docker-compose.yml", "up", "-d", "--force"
                execute "sudo", "docker-compose", "-f", "docker-compose.yml", "exec", "-T", "symfony", "php", "bin/console", "doctrine:migrations:migrate"
            end
        end
    end
end

# Bypass Composer
namespace :composer do
    task :run do
        execute ""
    end
end

namespace :deploy do
    desc 'Starting deployement'
    after :publishing, "symfony:migrate"
    after :publishing, "symfony:realt"
end