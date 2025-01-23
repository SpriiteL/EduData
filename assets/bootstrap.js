import { Application } from 'stimulus';
import { definitionsFromContext } from 'stimulus/webpack-helpers';

// Si tu utilises Symfony Webpack Encore, tu peux aussi avoir besoin de ce bridge (facultatif selon ton projet Symfony)


const application = Application.start();
const context = require.context('./controllers', true, /_controller\.js$/);
application.load(definitionsFromContext(context));
