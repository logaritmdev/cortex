![Cortex](https://github.com/logaritmdev/cortex/raw/master/logo.png)

Cortex is a WordPress plugin that eases the process of creating pages using blocks.

## What's a block?

A block is self-contained reusable site component. A block contains its template, fields definition, scripts, and styles. They are also stored locally which means they will play nice with your VCS.

## How can I build a page using blocks?

You basically select the block from a list and edit its content. Don't forget to update the page once you're done.

![Usage](https://github.com/logaritmdev/cortex/raw/master/doc/1.gif)

## How can I add blocks ?
From the `Cortex` menu, select `Blocks` and `Add New`. From there, you'll be able to define the block fields and edit your templates, stylesheet and scripts, if necessary. Note that the templates are Twig files using the Timber library. The style file supports the SCSS syntax and is automatically compiled when the block is saved.

![How](https://github.com/logaritmdev/cortex/raw/master/doc/2.gif)

Once completed you can go back to the page editor and use your newly created block.

![Done](https://github.com/logaritmdev/cortex/raw/master/doc/3.gif)

The block content will be saved in the `blocks` folder of your theme and the structure will look like this:

![Folder](https://github.com/logaritmdev/cortex/raw/master/doc/block.png)