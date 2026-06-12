<?php

declare(strict_types=1);

use Baconfy\Core\Module;

it('serializes the url as null when the entry route does not exist', function () {
    $module = new Module(name: 'ghost', routeName: 'ghost.index');

    expect($module->toArray()['url'])->toBeNull();
});
