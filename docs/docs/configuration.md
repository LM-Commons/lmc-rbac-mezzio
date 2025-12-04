---
sidebar_label: Configuration
sidebar_position: 5
title: Configuring LmcRbacMezzio
---

LmcRbacMezzio is configured via the `lmc_rbac` key in the application config. 

This is typically achieved by creating 
a `config/autoload/lmcrbac.global.php` file. A sample configuration file is provided in the `config/` folder.

:::warning
LmcRbacMezzio and LmcRbac share the same config key `'lmc_rbac'`. This allow to configure LmcRbacMezzio by adding its specific
configs to the same configuration file as LmcRbac.
:::

## Reference

| Key                     | Type            | Values                                                                             | Description                                                                                                                                                  |
|-------------------------|-----------------|------------------------------------------------------------------------------------|--------------------------------------------------------------------------------------------------------------------------------------------------------------|
| `guards`                | array           | `[]`                                                                               | Defines the guards to implement.<br/>Refer to the section on [Guards](guards.md).                                                                            |
| 'protection_policy'     | string          | - `GuardInterface::POLICY_ALLOW` **(default)**<br/>- `GuardInterface::POLICY_DENY` | Defines the protection policy that guards will use when no rules are specified for the role.<br/>Refer to the [Guards](guards.md#protection-policy) section. |
| 'unauthorized_strategy' | array&nbsp;map  | Defaults to `[]`                                                                   | Defines the configuration for the UnAuthorized strategy.<br/>Refer to the [Unauthorized Strategy](strategies#unauthorizedstrategy) section.                  | 
| 'redirect_strategy'     | array&nbsp;map  | Defaults to `[]`                                                                   | Defines the configuration for the Redirect strategy.<br/>Refer to the [Redirect Strategy](strategies#redirectstrategy) section.                              |
| 'guard_manager'         | array&nbsp;map  | Defaults to `[]`                                                                   | Guard Manager Plugin Manager configuration.<br/>Must follow a Service Manager configuration.                                                                 |
