import tornado.web
import tornado.ioloop
import tornado.httpserver
import tornado.options
from tornado.options import options, define

define('port', default=8233, help='run on the given port', type=int)
define('address', default='0.0.0.0', help='binding at given address', type=str)

from sshop import Application

def main():
    tornado.options.parse_command_line()
    server = tornado.httpserver.HTTPServer(Application())
    server.listen(options.port, options.address)
    print 'slog server started: <http://%s:%s>' % (options.address, options.port)
    tornado.ioloop.IOLoop.instance().start()


if __name__ == '__main__':
    main()