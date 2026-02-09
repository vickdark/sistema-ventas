<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$user = User::where('email', 'admin@multitenancy.test')->first();
if ($user) {
    $user->password = Hash::make('admin123');
    $user->save();
    echo "Contraseña para admin@multitenancy.test actualizada a: admin123\n";
} else {
    echo "Usuario no encontrado.\n";
}
