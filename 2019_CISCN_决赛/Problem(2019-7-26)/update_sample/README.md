### update.tar.gz目录结构
```
# tree update/
update/
├── some_files
└── update.sh
```

### update目录打包为update.tar.gz命令
```
tar zcvf update.tar.gz update
```

### 注意事项
+ web目录请多留意Dockerfile文件
+ 修改的原则尽可能只在漏洞相关的文件上，请勿更改其他文件
+ 执行脚本的文件名必须是update.sh
+ update.sh的执行权限，平台解包后自动对其`chmod +x`
+ 请勿添加无关命令，所有上传包都会备份。
+ 请注意耗时