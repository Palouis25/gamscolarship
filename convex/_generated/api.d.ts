/* eslint-disable */
/**
 * Generated `api` utility.
 *
 * THIS CODE IS AUTOMATICALLY GENERATED.
 *
 * To regenerate, run `npx convex dev`.
 * @module
 */

import type * as crons from "../crons.js";
import type * as discovery from "../discovery.js";
import type * as duplicates from "../duplicates.js";
import type * as ingestion from "../ingestion.js";
import type * as lib_deadline from "../lib/deadline.js";
import type * as lib_http from "../lib/http.js";
import type * as lib_linkCheck from "../lib/linkCheck.js";
import type * as lib_urlSafety from "../lib/urlSafety.js";
import type * as maintenance from "../maintenance.js";
import type * as rssIngestion from "../rssIngestion.js";
import type * as scholarships from "../scholarships.js";
import type * as seed from "../seed.js";
import type * as seedData from "../seedData.js";
import type * as sourceRegistry from "../sourceRegistry.js";
import type * as sources_erasmusMundusAdapter from "../sources/erasmusMundusAdapter.js";
import type * as sources_manualAdapter from "../sources/manualAdapter.js";
import type * as sources_officialSiteAdapter from "../sources/officialSiteAdapter.js";
import type * as sources_officialSiteSources from "../sources/officialSiteSources.js";
import type * as sources_registry from "../sources/registry.js";
import type * as sources_sitemap from "../sources/sitemap.js";
import type * as sources_types from "../sources/types.js";
import type * as validation from "../validation.js";

import type {
  ApiFromModules,
  FilterApi,
  FunctionReference,
} from "convex/server";

declare const fullApi: ApiFromModules<{
  crons: typeof crons;
  discovery: typeof discovery;
  duplicates: typeof duplicates;
  ingestion: typeof ingestion;
  "lib/deadline": typeof lib_deadline;
  "lib/http": typeof lib_http;
  "lib/linkCheck": typeof lib_linkCheck;
  "lib/urlSafety": typeof lib_urlSafety;
  maintenance: typeof maintenance;
  rssIngestion: typeof rssIngestion;
  scholarships: typeof scholarships;
  seed: typeof seed;
  seedData: typeof seedData;
  sourceRegistry: typeof sourceRegistry;
  "sources/erasmusMundusAdapter": typeof sources_erasmusMundusAdapter;
  "sources/manualAdapter": typeof sources_manualAdapter;
  "sources/officialSiteAdapter": typeof sources_officialSiteAdapter;
  "sources/officialSiteSources": typeof sources_officialSiteSources;
  "sources/registry": typeof sources_registry;
  "sources/sitemap": typeof sources_sitemap;
  "sources/types": typeof sources_types;
  validation: typeof validation;
}>;

/**
 * A utility for referencing Convex functions in your app's public API.
 *
 * Usage:
 * ```js
 * const myFunctionReference = api.myModule.myFunction;
 * ```
 */
export declare const api: FilterApi<
  typeof fullApi,
  FunctionReference<any, "public">
>;

/**
 * A utility for referencing Convex functions in your app's internal API.
 *
 * Usage:
 * ```js
 * const myFunctionReference = internal.myModule.myFunction;
 * ```
 */
export declare const internal: FilterApi<
  typeof fullApi,
  FunctionReference<any, "internal">
>;

export declare const components: {};
