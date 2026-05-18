export function filterSellListings<T extends { listing_mode?: string }>(
  items: T[] | null | undefined,
): T[] {
  if (!Array.isArray(items)) return [];
  return items.filter((item) => item.listing_mode !== "donate");
}
