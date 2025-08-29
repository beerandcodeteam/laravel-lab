<?php

namespace App\Console\Commands;

use Amp\Http\Server\DefaultErrorHandler;
use Amp\Http\Server\Router;
use Amp\Http\Server\SocketHttpServer;
use Amp\Log\ConsoleFormatter;
use Amp\Log\StreamHandler;
use Amp\Websocket\Server\AllowOriginAcceptor;
use Amp\Websocket\Server\Websocket;
use App\Services\TwilioCallHandlerService;
use Illuminate\Console\Command;
use Monolog\Logger;
use Amp\Socket;
use function Amp\ByteStream\getStdout;
use function Amp\trapSignal;

class CallWebSocket extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:call-web-socket';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $logHandler = new StreamHandler(getStdout());
        $logHandler->setFormatter(new ConsoleFormatter());
        $logger = new Logger('server');
        $logger->pushHandler($logHandler);

        $server = SocketHttpServer::createForDirectAccess($logger);

        $server->expose(new Socket\InternetAddress('0.0.0.0', 1337));
        $server->expose(new Socket\InternetAddress('[::1]', 1337));

        $errorHandler = new DefaultErrorHandler();

        $acceptor = new AllowOriginAcceptor(
            [null]
        );

        $clientHandler = new TwilioCallHandlerService();

        $websocket = new Websocket($server, $logger, $acceptor, $clientHandler);

        $router = new Router($server, $logger, $errorHandler);
        $router->addRoute('GET', '/call', $websocket);

        $server->start($router, $errorHandler);

        // Espera SIGINT/SIGTERM
        $signal = trapSignal([SIGINT, SIGTERM]);
        $logger->info(sprintf("Received signal %d, stopping HTTP server", $signal));
        $server->stop();
    }
}
