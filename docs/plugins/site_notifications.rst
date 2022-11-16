Site Notifications
==================

The Site notifications plugin offers a way for your users to keep up to date with what's happening on your community by sending 
a on-site notification.

Features
--------

- Get a notification when content is posted on the community
- Unread notifications will automatically be marked as read when you view the content it relates to
- Notifications will automatically be removed if the content it relates to is removed
- Plugin settings are available to automatically cleanup unread/read notifications

Note for developers
-------------------

The cron based cleanup of (un)read site notifications removes the entities directly from the database.
It isn't using ``$entity->delete()`` to help with performance. This means that no events are triggered for 
the entities which are removed during the cleanup.
