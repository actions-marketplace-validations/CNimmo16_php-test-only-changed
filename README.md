# Stop wasting time in CI running tests that you know aren't failing!

This Github action allows you to execute your PHP unit tests more quickly in CI, by running only tests which are related to files changed since the last successful workflow run.

In other words, we only run the tests which *may* actually be failing.

This package is inspired by the --onlyChanged flag in [Jest](https://github.com/jestjs/jest).

We use [Paratest](https://github.com/paratestphp/paratest) as the test runner for blazingly fast parallel execution.

## Prerequisites

The action should be used in a Github actions workflow with PHP and composer already set up.

## Usage

See an [example workflow](./.github/workflows/example.yml).

**Supported arguments:**

- `branch-name` Name of branch to scan for last successful workflow run. Defaults to current branch.
- `workflow-id` Name of workflow to check for success. Defaults to current workflow.
- `tests-directory` The directory where the tests are located relative to the root. Defaults to tests.
- `ignore-paths` Files to ignore when determining which files to run. Defaults to `vendor,node_modules,_ide_helper.php`

## How it works

The package builds a dependency graph for each of your test files, and then compares this to the files changed since your tests last passed successfully.

If the dependency graph includes the file being checked, the file is included in the test run.

We then generate a `phpunit.xml` on the fly, based on your existing `phpunit.xml`, with a testsuite specified for each of the relevant test files, as Paratest doesn't support running specific files within a larger test suite.

## Caveats

- Currently only supports PHP namespaces via "use" imports. Dynamic imports are not detected.