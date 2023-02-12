<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Psr\Log\LoggerInterface;
use App\Mail\OtpEmail;
use Bschmitt\Amqp\Amqp;
use Bschmitt\Amqp\Consumer;
use PhpAmqpLib\Message\AMQPMessage;

class ConsumerCommand extends Command
{
    use DispatchesJobs;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'broker:consume';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs a AMQP consumer that defers work to the Laravel queue worker';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(Amqp $consumer, LoggerInterface $logger): bool
    {
        $logger->info('Listening for messages...');

        $consumer->consume(
            'notification.service',
            function (AMQPMessage $message, Consumer $resolver) use ($logger): void {
                $logger->info('Consuming message...');

                try {
                    $payload = json_decode($message->getBody(), true, 512, JSON_THROW_ON_ERROR);
                    $this->validateMessage($payload);
                    $logger->info('Message received', $payload);
                    \Mail::to($payload['email'])->send(new OtpEmail($payload));
                    $logger->info('Message handled.');
                    $resolver->acknowledge($message);
                } catch (Exception $exception) {
                    $logger->error('Message failed validation.');
                    $resolver->reject($message);
                } catch (Exception $exception) {
                    $logger->error('Message is not valid JSON.');
                    $resolver->reject($message);
                }
            }
        );

        $logger->info('Consumer exited.');

        return true;
    }

    private function validateMessage(array $payload): void
    {
        \Log::info($payload);
    }
}
