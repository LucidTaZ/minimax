# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## Unreleased
### Added
- Support for PHP 7.3 is added. No code change was done for this, just QA.

### Removed
- Support for PHP 7.0 is dropped, since it's no longer officially supported.

### Changed
- (Dev) dependencies have been updated.

## [0.2.0] - 2017-07-26
### Added
- Alpha-beta pruning for faster results
- Analytics for the decision evaluations are now available from the `Engine`. It
  allows the caller to get information like number of evaluated moves.

### Changed
- GameState `decide()` now returns the new GameState immediately, instead of a
  `Decision` object that needs to be applied separately. It's needed anyway for 
  evaluation so this is one step shorter.
- The engine code now better represents the decision tree in its object graph.

## [0.1.0] - 2016-05-11
Initial release.