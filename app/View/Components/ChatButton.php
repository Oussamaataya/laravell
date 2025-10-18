<?php

namespace App\View\Components;

use Illuminate\View\Component;

class ChatButton extends Component
{
    public bool $show;
    public $messages;

    /**
     * Create a new component instance.
     *
     * @param bool $show
     * @param mixed $messages
     */
    public function __construct(bool $show = false, $messages = [])
    {
        $this->show = $show;
        $this->messages = $messages;
    }
    public function render()
    {
        return view('components.chat-button');
    }
}