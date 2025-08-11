<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component {
    public function logout(Logout $logout): void
    {
        $logout(); // This invokes the Logout action class

        $this->redirect('/login', navigate: true);
    }
};
?>