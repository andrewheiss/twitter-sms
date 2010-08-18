desc "Deploy to live server"
task :deploy do
  # rsync "nfsn:/home/public/twitter-sms"
  rsync "sms:/home/heisssms/www/twitter-sms"
end

def rsync(location)
  sh "rsync -rtvz --exclude-from '.rsync' --delete --stats --progress . #{location}"
end