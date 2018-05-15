<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class BuildWebsite extends Command
{

    protected $signature = 'website:build {domain} {theme}';

    protected $description = 'build a blog sites';

    public function __construct()
    {
        parent::__construct();
    }


    public function handle()
    {
        $domain = $this->argument('domain');
        $theme = $this->argument('theme');
        $this->buildBlog($domain, $theme);
    }

    private function buildBlog($domain, $theme)
    {
        // 复制博客源文件
        shell_exec("cp -r /home/www/www.eciggadget.com /home/www/$domain");

        // 修改wordpress配置
        shell_exec("sed -i 's/www.eciggadget.com/$domain/' /home/www/$domain/wp-config.php");

        // 新增vhost
        $subDomain = substr($domain, 4);
        shell_exec("cp /etc/nginx/conf.d/www.eciggadget.com.conf /etc/nginx/conf.d/$domain.conf");
        shell_exec("sed -i 's/www.eciggadget.com/$domain/' /etc/nginx/conf.d/$domain.conf");
        shell_exec("sed -i 's/eciggadget.com/$subDomain/' /etc/nginx/conf.d/$domain.conf");

        // 安装主题
        shell_exec("cd /home/www/$domain && wp theme install $theme --activate");

        // 设置权限
        shell_exec("chown -R apache.apache /home/www/$domain");

        $this->info("$domain done");
    }

}
