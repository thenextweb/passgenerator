## 0.3.x

This new version includes a stricter type control of all the definitions in order to make it safer. Although this should be a safe update, **it may break** current installations if any of the typehints used on the functions are different from what your software is using, but you should take that as a chance to fix your app.

We tried to add all the docs from Apple into the library so they are accessible from modern IDEs. Lastly, we've integrated the [SafePHP functions](https://github.com/thecodingmachine/safe) and larastan on level 6 to avoid loose ends on the migration.

We hope everybody enjoy this new version.