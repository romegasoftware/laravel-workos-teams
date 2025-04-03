# WorkOS Teams for Laravel

This package extends the Laravel WorkOS integration with team + organization functionality. It provides a migration path for users transitioning from Jetstream Teams to WorkOS Organizations.

## Features

- Feature parity with Jetstream Teams
- Can be integrated into new or existing Laravel applications
- Integrates with WorkOS Organizations
- Team invitation and member management
- Organization and user synchronization with WorkOS
- Livewire components for team management (optional)
- Webhooks for WorkOS events
- Repository pattern for modular implementation

## Docs

1. [Installation and Configuration](docs/install.md)
2. [Webhooks](docs/webhooks.md)
3. [Livewire Components](docs/livewire-components.md)
4. [Customization](docs/customizing-models.md)
5. [Coming soon: Console Commands](docs/console.md)

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Jetstream Feature Parity

As Jetstream is being replaced by [Starter Kits](https://laravel.com/starter-kits), and [Laravel's documentation](https://laravel.com/docs/12.x/starter-kits#workos) is encouraging developers to utilize WorkOS, I noticed a gap with developers wanting to use the Teams functionality they once had with Jetstream. My goal with building this package was to utilize the functionality WorkOS makes avaialable through Organizations, and pair it with the naming conventions we are used to with Jetstream. Therefore, we keep the "Teams" language in this package.

A few known differences with Jetstream:
- Permissions: We are only utilizing Roles currently. There is no Permissions implementation, though we do have plans to implement.
- Inertia: I would accept contributions providing an Inertia UI. I do not have a need for this myself right now, so I do not have active plans to implement.
- Features::profilePhotos: We do not have any current or planned implementation for profilePhotos at this time. This can easily be implemented directly on your User model while making the necessary UI adjustments to accommodate through the published components.
- Features::api: Jetstream provided an interface for managing Sanctum API tokens. I am of the mindset that this package should focus on Teams, and even to the extent that we provide Livewire components but do not require Livewire for implementation. Doing so, we are frontend agnostic. Since Jetstream just provided a UI wrapper for Sanctum, that could be managed in a separate package. No plans to implement.
- Profile Management: Profile Management is handled with the Laravel WorkOS starter kit.

If you notice any other differences that should be included, feel free to open an issue or a pull request.


TODO:

- [ ] Update documentation with screenshots
- [ ] Implement the console sync
- [ ]