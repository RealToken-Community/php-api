set :stages, ["staging", "circleci"]
set :repo_url, 'git@github.com:RealT-Community/RealT-API.git'

## Docker Config
namespace :docker do
    desc "Custom RealT jobs for containers/Symfony config"
    task :realt do
        on roles(:circleci) do
            within release_path do
                # Restart containers on Staging
                # TODO: Add docker builder
#                execute "chown", "-R", "realt:docker", "/home/realt/docker/api/circleci/releases", "/home/realt/docker/api/circleci/current"
#                execute "chmod", "+x", "/home/realt/docker/api/circleci/current/.circleci/continous_deployment.sh"
#                execute "runuser", "-l", "realt", "-c", "'cd /home/realt/docker/api/circleci/current && ./.circleci/continous_deployment.sh'"
               #execute "'cd /home/kurtest/docker/api/circleci/current && ./continous_deployment.sh'"
            end
        end

    end
end

namespace :deploy do
    desc 'Starting deployement'
#    after :publishing, "symfony:migrate"
    after :publishing, "docker:realt"
end
