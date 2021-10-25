# Installation
Run `make build` in order to install all application dependencies (you must have Docker installed).

For more commands, type `make help`

# Application purpose
Simple training with sync and async events using the Messenger component.

# Using the application
Go to `{PROJECT_FOLDER}/docs/endpoints` and you'll see a `cart.http` file containing all the requests that actually the app can handle.

## Changelog
- The application starts with a simple endpoint to simulate the user creation.
- Once the user was created, we want to generate a coupon to greet the new user.
  - This could be done by simply calling the `CouponGenerator` at the `CreateUser` service, but it creates a coupling between the coupon generation and the user creation (breaking the OCP in the process).
  - We solve this problem by publishing the domain event `UserAdded` and letting the subscribers reacting to that action.
  - In this first approach we use a `SyncEventBus` since it is easier to implement.

