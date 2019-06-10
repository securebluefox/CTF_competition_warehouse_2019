# -*- coding:utf-8 -*-
from flask import Flask,render_template_string,request
from gevent import monkey
from gevent.pywsgi import WSGIServer
monkey.patch_all()

app = Flask(__name__)


@app.errorhandler(404)
def page_not_found(e):
    if "__subclasses__()[40]" in request.url:
        return "<h1>file is not allowed<h1>"
    elif "os" in request.url:
        return "<h1>os is not allowed</h1>"
    elif "__subclasses__()[71]"  in request.url:
        return "<h1>'site._Printer' is not allowed</h1>"
    elif  "__subclasses__()[76]" in request.url:
        return "<h1>'site.Quitter' is not allowed</h1>"
    elif "eval" in request.url:
        return "eval is not allowed"
    template = '''
    <meta http-equiv="content-type" content="text/html;charset=utf-8">
    <center>
        <h1>Sorry! 404 Page NOT FOUND
        <h3>from: %s</h3>
        <a href="javascript:history.go(-1);" >go back</a>
    </center>
    ''' % (request.url)
    return render_template_string(template), 404


http_server = WSGIServer(('', 5000), app)
http_server.serve_forever()

