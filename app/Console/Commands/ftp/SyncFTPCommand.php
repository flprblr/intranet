<?php

namespace App\Console\Commands\ftp;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use phpseclib3\Net\SFTP;
use phpseclib3\Crypt\PublicKeyLoader;

class SyncFTPCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ftp:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $ftpSources = [
            'Adidas', 'Converse', 'FollowUP', 'Puma', 'Gfk', 'Nike', 'Reebok', 'ShopperTrack', 'Apple'
            //'Adidas'
        ];

        $startTime = Carbon::now();

        foreach ($ftpSources as $source) {
            $methodName = 'sync' . $source;
            $this->$methodName();
            $this->logExecutionTime($methodName, $startTime);
        }

        $endTime = Carbon::now();
        $executionTime = $endTime->diffInSeconds($startTime);
        Log::info('Total execution time: ' . $executionTime . ' seconds');

        return Command::SUCCESS;
    }

    private function syncAdidas()
    {
        try {
            $remoteDir = '/Belsport';
            $remoteCredentials = [
                'host' => 'ftp.adidas-group.com',
                'username' => 'glbl-sales-SOspecialist',
                'password' => 'TRwTaRr1MO6sS',
            ];

            $remoteDirectory = "/";
            $remoteFiles = Storage::disk('adidas')->files($remoteDirectory);

            // Verificar si no hay archivos para procesar
            if (empty($remoteFiles)) {
                Log::channel('adidas')->info('No hay archivos para procesar en Adidas FTP.');
                return; // Salir de la función si no hay archivos
            }

            $sftp = new SFTP($remoteCredentials['host']);

            if (!$sftp->login($remoteCredentials['username'], $remoteCredentials['password'])) {
                throw new \Exception('Login failed to Adidas FTP');
            }

            foreach ($remoteFiles as $remoteFile) {
                $fileName = basename($remoteFile);
                $remoteFilePath = $remoteDir . '/' . $fileName;

                $fileContent = Storage::disk('adidas')->get($remoteFile);

                if ($this->uploadFileToSftp($sftp, $remoteFilePath, $fileContent)) {
                    Log::channel('adidas')->info('Uploaded successfully: ' . $fileName);
                    // Eliminar el archivo remoto después de la carga exitosa
                    Storage::disk('adidas')->delete($remoteFile);
                } else {
                    Log::channel('adidas')->error('Error uploading: ' . $fileName);
                }
            }

            $sftp->disconnect();
        } catch (\Exception $e) {
            Log::channel('adidas')->error('Error transferring files: ' . $e->getMessage());
        }
    }

    private function syncConverse()
    {
        $server = "ftp.comercialdepor.cl";
        $username = "belsport_ventas@comercialdepor.cl";
        $password = "sHxD#j#NCnpy";
        try {
            $remoteDirectory = "/";
            $remoteFiles = Storage::disk('converse')->files($remoteDirectory);

            if (empty($remoteFiles)) {
                Log::channel('converse')->info('No hay archivos para procesar en Converse FTP.');
                return; // Salir de la función si no hay archivos
            }

            $ftp = ftp_connect($server);

            if (!$ftp) {
                throw new \Exception('Failed to connect to FTP server');
            }

            if (!ftp_login($ftp, $username, $password)) {
                throw new \Exception('Login failed');
            }

            ftp_pasv($ftp, true);

            if (ftp_chdir($ftp, "/")) {
                foreach ($remoteFiles as $remoteFile) {
                    $fileContent = Storage::disk('converse')->get($remoteFile);
                    // Genera un archivo temporal local para almacenar el contenido descargado
                    $localTempFile = tempnam(sys_get_temp_dir(), 'file');
                    file_put_contents($localTempFile, $fileContent);

                    if (ftp_put($ftp, basename($remoteFile), $localTempFile, FTP_BINARY)) {
                        Log::channel('converse')->info('Uploaded file: ' . basename($remoteFile));
                        // Elimina el archivo temporal local después de la carga exitosa
                        unlink($localTempFile);
                        Storage::disk('converse')->delete($remoteFile);
                    } else {
                        Log::channel('converse')->error('Failed to upload file: ' . basename($remoteFile));
                    }
                }
            } else {
                Log::channel('converse')->error('Failed to change directory');
            }

            ftp_close($ftp);
        } catch (\Exception $e) {
            Log::channel('converse')->error('Error uploading file: ' . $e->getMessage());
        }
    }

    private function syncFollowUP()
    {
        $server = "ftp.fupbi.com";
        $username = "followup_belsport";
        $password = "ODk5M2U0NjZjM";
        try {
            $remoteDirectory = "/";
            $remoteFiles = Storage::disk('followup')->files($remoteDirectory);

            if (empty($remoteFiles)) {
                Log::channel('followup')->info('No hay archivos para procesar en FollowUP FTP.');
                return; // Salir de la función si no hay archivos
            }

            $ftp = ftp_connect($server);

            if (!$ftp) {
                throw new \Exception('Failed to connect to FTP server');
            }

            if (!ftp_login($ftp, $username, $password)) {
                throw new \Exception('Login failed');
            }

            ftp_pasv($ftp, true);

            if (ftp_chdir($ftp, "/upload")) {
                foreach ($remoteFiles as $remoteFile) {
                    $fileContent = Storage::disk('followup')->get($remoteFile);
                    // Genera un archivo temporal local para almacenar el contenido descargado
                    $localTempFile = tempnam(sys_get_temp_dir(), 'file');
                    file_put_contents($localTempFile, $fileContent);

                    if (ftp_put($ftp, basename($remoteFile), $localTempFile, FTP_BINARY)) {
                        Log::channel('followup')->info('Uploaded file: ' . basename($remoteFile));
                        // Elimina el archivo temporal local después de la carga exitosa
                        unlink($localTempFile);
                        Storage::disk('followup')->delete($remoteFile);
                    } else {
                        Log::channel('followup')->error('Failed to upload file: ' . basename($remoteFile));
                    }
                }
            } else {
                Log::channel('followup')->error('Failed to change directory');
            }

            ftp_close($ftp);
        } catch (\Exception $e) {
            Log::channel('followup')->error('Error uploading file: ' . $e->getMessage());
        }
    }

    private function syncPuma()
    {
        try {
            $remoteDir = '/Shared';
            $remoteCredentials = [
                'host' => 'fiona.mypuma.net',
                'username' => 'srv_scl_b2b_belsport',
                'password' => '5-7>MwcLJdpBXsJI}6k>k52Lq',
            ];

            $remoteDirectory = "/";
            $remoteFiles = Storage::disk('puma')->files($remoteDirectory);

            if (empty($remoteFiles)) {
                Log::channel('puma')->info('No hay archivos para procesar en Puma FTP.');
                return; // Salir de la función si no hay archivos
            }

            $sftp = new SFTP($remoteCredentials['host']);

            if (!$sftp->login($remoteCredentials['username'], $remoteCredentials['password'])) {
                throw new \Exception('Login failed to Puma FTP');
            }

            foreach ($remoteFiles as $remoteFile) {
                $fileName = basename($remoteFile);
                $remoteFilePath = $remoteDir . '/' . $fileName;

                $fileContent = Storage::disk('puma')->get($remoteFile);

                if ($this->uploadFileToSftp($sftp, $remoteFilePath, $fileContent)) {
                    Log::channel('puma')->info('Uploaded successfully: ' . $fileName);
                    // Eliminar el archivo remoto después de la carga exitosa
                    Storage::disk('puma')->delete($remoteFile);
                } else {
                    Log::channel('puma')->error('Error uploading: ' . $fileName);
                }
            }

            $sftp->disconnect();
        } catch (\Exception $e) {
            Log::channel('puma')->error('Error transferring files: ' . $e->getMessage());
        }
    }

    private function syncGfk()
    {
        try {
            $remoteCredentials = [
                'host' => 'gimftp.gfk.com',
                'username' => 'GFK002673',
                'password' => 'Iy4?[sOwoz(UUd9]Xk[R',
            ];

            $remoteDirectory = "/";
            $remoteFiles = Storage::disk('gfk')->files($remoteDirectory);

            if (empty($remoteFiles)) {
                Log::channel('gfk')->info('No hay archivos para procesar en GFK FTP.');
                return; // Salir de la función si no hay archivos
            }


            $sftp = new SFTP($remoteCredentials['host']);

            if (!$sftp->login($remoteCredentials['username'], $remoteCredentials['password'])) {
                throw new \Exception('Login failed to Gfk FTP');
            }

            foreach ($remoteFiles as $remoteFile) {
                $fileName = basename($remoteFile);
                $remoteFilePath = '/' . $fileName;

                $fileContent = Storage::disk('gfk')->get($remoteFile);

                if ($this->uploadFileToSftp($sftp, $remoteFilePath, $fileContent)) {
                    Log::channel('gfk')->info('Uploaded successfully: ' . $fileName);
                    // Eliminar el archivo remoto después de la carga exitosa
                    Storage::disk('gfk')->delete($remoteFile);
                } else {
                    Log::channel('gfk')->error('Error uploading: ' . $fileName);
                }
            }

            $sftp->disconnect();
        } catch (\Exception $e) {
            Log::channel('gfk')->error('Error transferring files: ' . $e->getMessage());
        }
    }

    private function syncNike()
    {
        try {
            $remoteCredentials = [
                'host' => 's-bd7d14ca7fde478fb.server.transfer.us-east-1.amazonaws.com',
                'username' => 'EQCL_USRFTPBSG005',
                'password' => 'jx>BFLxH<2PMc{@',
            ];

            $remoteDirectory = "/";
            $remoteFiles = Storage::disk('nike')->files($remoteDirectory);

            if (empty($remoteFiles)) {
                Log::channel('nike')->info('No hay archivos para procesar en Nike FTP.');
                return; // Salir de la función si no hay archivos
            }

            $sftp = new SFTP($remoteCredentials['host']);

            if (!$sftp->login($remoteCredentials['username'], $remoteCredentials['password'])) {
                throw new \Exception('Login failed to Nike FTP');
            }

            foreach ($remoteFiles as $remoteFile) {
                $fileName = basename($remoteFile);
                $remoteFilePath = '/s3-equinox-sellout/BelsportGroup/' . $fileName;

                $fileContent = Storage::disk('nike')->get($remoteFile);

                if ($this->uploadFileToSftp($sftp, $remoteFilePath, $fileContent)) {
                    Log::channel('nike')->info('Uploaded successfully: ' . $fileName);
                    // Eliminar el archivo remoto después de la carga exitosa
                    Storage::disk('nike')->delete($remoteFile);
                } else {
                    Log::channel('nike')->error('Error uploading: ' . $fileName);
                }
            }

            $sftp->disconnect();
        } catch (\Exception $e) {
            Log::channel('nike')->error('Error transferring files: ' . $e->getMessage());
        }
    }

    private function syncReebok()
    {
        $server = "104.215.114.61";
        $username = "yanekenreebok";
        $password = "yaneken2023";
        try {
            $remoteDirectory = "/";
            $remoteFiles = Storage::disk('reebok')->files($remoteDirectory);

            if (empty($remoteFiles)) {
                Log::channel('reebok')->info('No hay archivos para procesar en Reebok FTP.');
                return; // Salir de la función si no hay archivos
            }

            $ftp = ftp_connect($server);

            if (!$ftp) {
                throw new \Exception('Failed to connect to FTP server');
            }

            if (!ftp_login($ftp, $username, $password)) {
                throw new \Exception('Login failed');
            }

            ftp_pasv($ftp, true);

            if (ftp_chdir($ftp, "/")) {
                foreach ($remoteFiles as $remoteFile) {
                    $fileContent = Storage::disk('reebok')->get($remoteFile);
                    // Genera un archivo temporal local para almacenar el contenido descargado
                    $localTempFile = tempnam(sys_get_temp_dir(), 'file');
                    file_put_contents($localTempFile, $fileContent);

                    if (ftp_put($ftp, basename($remoteFile), $localTempFile, FTP_BINARY)) {
                        Log::channel('reebok')->info('Uploaded file: ' . basename($remoteFile));
                        // Elimina el archivo temporal local después de la carga exitosa
                        unlink($localTempFile);
                        Storage::disk('reebok')->delete($remoteFile);
                    } else {
                        Log::channel('reebok')->error('Failed to upload file: ' . basename($remoteFile));
                    }
                }
            } else {
                Log::channel('reebok')->error('Failed to change directory');
            }

            ftp_close($ftp);
        } catch (\Exception $e) {
            Log::channel('reebok')->error('Error uploading file: ' . $e->getMessage());
        }
    }

    private function syncShopperTrack()
    {
        try {
            $remoteCredentials = [
                'host' => 'data.shoppertrak.com',
                'username' => 'reifschneider',
                'password' => '$q4HjsZ&UBaz^U',
            ];

            $remoteDirectory = "/";
            $remoteFiles = Storage::disk('shoppertrack')->files($remoteDirectory);

            if (empty($remoteFiles)) {
                Log::channel('shoppertrack')->info('No hay archivos para procesar en ShopperTrack FTP.');
                return; // Salir de la función si no hay archivos
            }

            $sftp = new SFTP($remoteCredentials['host']);

            if (!$sftp->login($remoteCredentials['username'], $remoteCredentials['password'])) {
                throw new \Exception('Login failed to ShopperTrack FTP');
            }

            foreach ($remoteFiles as $remoteFile) {
                $fileName = basename($remoteFile);
                $remoteFilePath = '/' . $fileName;

                $fileContent = Storage::disk('shoppertrack')->get($remoteFile);

                if ($this->uploadFileToSftp($sftp, $remoteFilePath, $fileContent)) {
                    Log::channel('shoppertrack')->info('Uploaded successfully: ' . $fileName);
                    // Eliminar el archivo remoto después de la carga exitosa
                    Storage::disk('shoppertrack')->delete($remoteFile);
                } else {
                    Log::channel('shoppertrack')->error('Error uploading: ' . $fileName);
                }
            }

            $sftp->disconnect();
        } catch (\Exception $e) {
            Log::channel('shoppertrack')->error('Error transferring files: ' . $e->getMessage());
        }
    }

    private function syncApple()
    {
        $remoteHost = 'b2b-sftp.apple.com';
        $username = 'yaneken_cl';
        $privateKeyPath = '/home/intranet/id_rsa';
        $remotePort = 22;
        $sessionTimeout = 10;
        $channelTimeout = 5;

        try {
            $sftp = new SFTP($remoteHost, $remotePort, $sessionTimeout);

            $remoteDirectory = "/";
            $remoteFiles = Storage::disk('apple')->files($remoteDirectory);

            if (empty($remoteFiles)) {
                Log::channel('apple')->info('No hay archivos para procesar en Apple FTP.');
                return; // Salir de la función si no hay archivos
            }

            $privateKey = PublicKeyLoader::load(file_get_contents($privateKeyPath));

            if (!$sftp->login($username, $privateKey)) {
                throw new \Exception('Login failed to Apple FTP');
            }

            $sftp->setTimeout($channelTimeout);
            $sftp->chdir('out/reporting');

            foreach ($remoteFiles as $remoteFile) {
                $fileContent = Storage::disk('apple')->get($remoteFile);
                // Generar un archivo temporal local para almacenar el contenido descargado
                $localTempFile = tempnam(sys_get_temp_dir(), 'file');
                file_put_contents($localTempFile, $fileContent);
                if (!$sftp->put(basename($remoteFile), $localTempFile, SFTP::SOURCE_LOCAL_FILE)) {
                    Log::channel('apple')->info('File upload failed: ' . basename($remoteFile));
                } else {
                    Log::channel('apple')->info('File uploaded successfully: ' . basename($remoteFile));
                    // Eliminar el archivo temporal local después de la carga
                    unlink($localTempFile);
                    Storage::disk('apple')->delete($remoteFile);
                }
            }

            $sftp->disconnect();
            Log::info('Synchronization successful');
        } catch (\Exception $e) {
            Log::channel('apple')->info('Error: ' . $e->getMessage());
        }
    }

    private function logExecutionTime($methodName, $startTime)
    {
        $endTime = Carbon::now();
        $executionTime = $endTime->diffInSeconds($startTime);
        Log::info('FTP ' . $methodName . ': ' . $executionTime . ' seconds');
    }

    private function uploadFileToSftp($sftp, $remotePath, $content)
    {
        return $sftp->put($remotePath, $content);
    }
}
