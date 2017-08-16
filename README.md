# rm-woocommerce-order-exporter

## 安装
1. 将解压得到的rm-woocommerce-order-exporter-master目录直接放到wordpress安装目录下的wp-content/plugins/目录下
1. 修改rm-woocommerce-order-exporter-master目录的属主
`chown www:www -R .`
1. 进入wordpress后台,确认已经安装并启用woocommerce 3.1.1及以上版本
1. wordpress后台 > Plugins > Installed Plugins > RM Woocommerce order exporter > Activate来启用插件
![](/home/richardma/Projects/rm-woocommerce-order-exporter/images/1.png) 

## 使用
1. wordpress后台 > Woocommerce > order exporter
1. 如下图,填写好导出订单id,选择导出格式csv/excel,然后点击Export按钮即可导出指定的订单
![](/home/richardma/Projects/rm-woocommerce-order-exporter/images/2.png) 
1. 订单id支持3-5以及用,分割的多种格式