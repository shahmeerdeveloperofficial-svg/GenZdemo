<?php


namespace App\Traits;


trait General
{
    private function controlRedirection($request, $route, $model)
    {
        switch ($request) {
            case $request->has('save_and_add'):
                $this->showToastrMessage('success', trans('message.created', ['title' => $model]));
                return redirect()->back();
                break;

            case $request->has('save'):
                $this->showToastrMessage('success', trans('message.created', ['title' => $model]));
                return redirect()->route($route . '.index');
                break;

            case $request->has('update'):
                $this->showToastrMessage('success', trans('message.updated', ['title' => $model]));
                return redirect()->route($route . '.index');
                break;

            default:
                $this->showToastrMessage('success', trans('message.created', ['title' => $model]));
                return redirect()->route($route . '.index');
        }
    }

    private function showToastrMessage($type, $message)
    {
        switch ($type) {
            case 'success':
                return session()->flash('success', $message);
                break;
            case 'warning':
                return session()->flash('warning', $message);
                break;
            case 'error':
                return session()->flash('error', $message);
                break;
            default:
                return session()->flash('success', $message);
        }
    }
}
