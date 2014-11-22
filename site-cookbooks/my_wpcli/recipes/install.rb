# encoding: utf-8
# vim: ft=ruby expandtab shiftwidth=2 tabstop=2

require 'shellwords'


apache_site "000-default" do
  enable false
end

web_app "wordpress" do
  template "wordpress.conf.erb"
  docroot node[:wpcli][:wp_docroot]
  server_name node[:fqdn]
end

bash "create-ssl-keys" do
  user "root"
  group "root"
  cwd File.join(node[:apache][:dir], 'ssl')
  code <<-EOH
    openssl genrsa -out server.key 2048
    openssl req -new -key server.key -subj '/C=JP/ST=Wakayama/L=Kushimoto/O=My Corporate/CN=#{node[:fqdn]}' -out server.csr
    openssl x509 -in server.csr -days 365 -req -signkey server.key > server.crt
  EOH
  notifies :restart, "service[apache2]"
end


iptables_rule "wordpress-iptables"


# node.set_unless[:wpcli][:dbpassword] = secure_password

execute "mysql-install-wp-privileges" do
  command "/usr/bin/mysql -u root -p\"#{node[:mysql][:server_root_password]}\" < #{node[:mysql][:conf_dir]}/wp-grants.sql"
  action :nothing
end

template File.join(node[:mysql][:conf_dir], '/wp-grants.sql') do
  source "grants.sql.erb"
  owner node[:wpcli][:user]
  group node[:wpcli][:group]
  mode "0600"
  variables(
    :user     => node[:wpcli][:dbuser],
    :password => node[:wpcli][:dbpassword],
    :database => node[:wpcli][:dbname]
  )
  notifies :run, "execute[mysql-install-wp-privileges]", :immediately
end


execute "create wordpress database" do
  command "/usr/bin/mysqladmin -u root -p\"#{node[:mysql][:server_root_password]}\" create #{node[:wpcli][:dbname]}"
  not_if do
    # Make sure gem is detected if it was just installed earlier in this recipe
    require 'rubygems'
    Gem.clear_paths
    require 'mysql'
    m = Mysql.new("localhost", "root", node[:mysql][:server_root_password])
    m.list_dbs.include?(node[:wpcli][:dbname])
  end
  notifies :create, "ruby_block[save node data]", :immediately unless Chef::Config[:solo]
end

if node[:wpcli][:always_reset] == true then
    bash "wordpress-db-reset" do
      user node[:wpcli][:user]
      group node[:wpcli][:group]
      cwd File.join(node[:wpcli][:wp_docroot], node[:wpcli][:wp_dir])
      code "WP_CLI_CONFIG_PATH=#{Shellwords.shellescape(node[:wpcli][:config_path])} wp db reset --yes"
    end
end


bash "wordpress-core-install" do
  user node[:wpcli][:user]
  group node[:wpcli][:group]
  cwd File.join(node[:wpcli][:wp_docroot], node[:wpcli][:wp_dir])
  code <<-EOH
    WP_CLI_CONFIG_PATH=#{Shellwords.shellescape(node[:wpcli][:config_path])} wp core install \\
    --url=http://#{File.join(node[:wpcli][:wp_host], node[:wpcli][:wp_dir])} \\
    --title=#{Shellwords.shellescape(node[:wpcli][:title])} \\
    --admin_user=#{Shellwords.shellescape(node[:wpcli][:admin_user])} \\
    --admin_password=#{Shellwords.shellescape(node[:wpcli][:admin_password])} \\
    --admin_email=#{Shellwords.shellescape(node[:wpcli][:admin_email])}
  EOH
end


if node[:wpcli][:default_theme] != '' then
    bash "WordPress #{node[:wpcli][:default_theme]} activate" do
      user node[:wpcli][:user]
      group node[:wpcli][:group]
      cwd File.join(node[:wpcli][:wp_docroot], node[:wpcli][:wp_dir])
      code "WP_CLI_CONFIG_PATH=#{Shellwords.shellescape(node[:wpcli][:config_path])} wp theme activate #{File.basename(Shellwords.shellescape(node[:wpcli][:default_theme])).sub(/\..*$/, '')}"
    end
end



node[:wpcli][:options].each do |key, value|
  bash "Setting up WordPress option #{key}" do
    user node[:wpcli][:user]
    group node[:wpcli][:group]
    cwd File.join(node[:wpcli][:wp_docroot], node[:wpcli][:wp_dir])
    code "WP_CLI_CONFIG_PATH=#{Shellwords.shellescape(node[:wpcli][:config_path])} wp option update #{Shellwords.shellescape(key)} #{Shellwords.shellescape(value)}"
  end
end


if node[:wpcli][:rewrite_structure] then
  template File.join(node[:wpcli][:wp_docroot], node[:wpcli][:wp_home], '.htaccess') do
    source ".htaccess"
    owner node[:wpcli][:user]
    group node[:wpcli][:group]
    mode "0644"
  end
  bash "Setting up rewrite rules" do
    user node[:wpcli][:user]
    group node[:wpcli][:group]
    cwd File.join(node[:wpcli][:wp_docroot], node[:wpcli][:wp_dir])
    code "WP_CLI_CONFIG_PATH=#{Shellwords.shellescape(node[:wpcli][:config_path])} wp rewrite structure #{Shellwords.shellescape(node[:wpcli][:rewrite_structure])} --hard"
  end
end


