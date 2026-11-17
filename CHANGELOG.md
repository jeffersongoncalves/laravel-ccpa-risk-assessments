# Changelog

All notable changes to this project will be documented in this file.

## 1.0.0 - 2026-11-17

First release: CCPA/CPRA risk assessments (CCPA Regulations, Article 10) for Laravel, part of the laravel-ccpa suite.

- `RiskAssessment`: the processing, its necessity, and risks scored by likelihood × impact (1 to 3), with optional LINDDUN threats, safeguards and residual scores.
- The reviewer's opinion and approval, with a review scheduled for 3 years later (§7155). Editing an approved assessment sends it back to draft.
- `markSubmitted()` records the submission to the CPPA (§7157).
- `CcpaRiskAssessments::candidates()` lists processing activities that sell or share personal information, or process sensitive personal information, and have no assessment (§7150(b)). It links to laravel-ccpa-notices.
- `ccpa:risk-assessments [--json]` and `CcpaRiskAssessments::export()`.

## [Unreleased]
