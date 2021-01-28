set :repo_url, 'git@github.com:RealT-Community/RealT-API.git'

## Docker Config
namespace :docker do
    desc "Custom RealT jobs for containers/Symfony config"
    task :realt do
        on roles(:staging) do
            within release_path do
                # Restart containers on Staging
                execute "chown", "-R", "realt:docker", "/home/realt/docker/api/preprod/releases", "/home/realt/docker/api/preprod/current"
                execute "chmod", "+x", "/home/realt/docker/api/preprod/current/.circleci/continous_deployment.sh"
                execute "runuser", "-l", "realt", "-c", "'cd /home/realt/docker/api/preprod && ./current/.circleci/continous_deployment.sh'"
            end
        end

        on roles(:production) do
            within release_path do
                # Restart containers on Production
                execute "chown", "-R", "realt:docker", "/home/realt/docker/api/prod/releases", "/home/realt/docker/api/prod/current"
                execute "chmod", "+x", "/home/realt/docker/api/prod/current/.circleci/continous_deployment.sh"
                execute "runuser", "-l", "realt", "-c", "'cd /home/realt/docker/api/prod && ./current/.circleci/continous_deployment.sh'"
            end
        end
    end
end

namespace :deploy do
    desc 'Starting deployement'
#    after :publishing, "symfony:migrate"
    after :publishing, "docker:realt"
end