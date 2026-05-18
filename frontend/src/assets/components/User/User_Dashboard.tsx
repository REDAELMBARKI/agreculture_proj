import React, { useState, useEffect, useMemo } from "react";
import { useNavigate, useParams, Link } from "react-router-dom";
import api from "../../../services/api";
import { filterSellListings } from "../../../utils/sellOnly";
import "../../../css/user.css";

type ProductRow = Record<string, unknown>;

export default function User_Dashboard() {
  const navigate = useNavigate();
  const { id } = useParams();

  const [user, setUser] = useState<Record<string, unknown> | null>(null);
  const [allListings, setAllListings] = useState<ProductRow[]>([]);
  const [loadingListings, setLoadingListings] = useState<boolean>(true);
  const [recentFilter, setRecentFilter] = useState<"all" | "sale">("all");
  const [modalOpen, setModalOpen] = useState(false);
  const [modalImage, setModalImage] = useState<string | null>(null);

  useEffect(() => {
    const storedUser = localStorage.getItem("user");
    const token = localStorage.getItem("token");
    if (!storedUser || !token) {
      navigate("/login");
      return;
    }

    const parsedUser = JSON.parse(storedUser) as Record<string, unknown>;
    const uid = (parsedUser?.id || parsedUser?.user_ID) as number | undefined;
    if (!uid) {
      navigate("/login");
      return;
    }
    setUser(parsedUser);
  }, [id, navigate]);

  useEffect(() => {
    const uid = (user?.id || user?.user_ID) as number | undefined;
    const userSlug = (user?.slug as string | undefined) ?? undefined;
    const userKey = userSlug || (uid ? String(uid) : "");
    if (!userKey) return;

    setLoadingListings(true);
    api
      .get(`/api/user/${userKey}/announcements`)
      .then((res) => {
        const products = res.data?.products?.data || res.data?.products || [];
        setAllListings(filterSellListings(Array.isArray(products) ? products : []));
      })
      .catch((err) => console.error("Listings fetch error:", err))
      .finally(() => setLoadingListings(false));
  }, [user]);

  const displayedRows = useMemo(() => {
    let rows = allListings;
    if (recentFilter === "sale") {
      rows = allListings.filter((p) => p.listing_mode === "sell");
    }
    const sorted = [...rows].sort((a, b) => {
      const ta = a.created_at ? new Date(String(a.created_at)).getTime() : 0;
      const tb = b.created_at ? new Date(String(b.created_at)).getTime() : 0;
      return tb - ta;
    });
    return sorted.slice(0, 8);
  }, [recentFilter, allListings]);

  const thumbUrl = (d: ProductRow) => {
    const thumb = d.thumbnail as { url?: string; file_path?: string; path?: string } | undefined;
    if (!thumb) return null;
    if (thumb.url && String(thumb.url).startsWith("http")) return String(thumb.url);
    const path = thumb.file_path || thumb.path;
    if (!path) return null;
    const clean = String(path).replace(/^public\//, "").replace(/^\/+/, "");
    const base = import.meta.env.VITE_API_URL?.replace(/\/api\/?$/, "") || "http://127.0.0.1:8000";
    return `${base}/storage/${clean}`;
  };

  const stats = useMemo(() => {
    const total = allListings.length;
    const active = allListings.filter((p) => {
      const status = String(p.status ?? "").toLowerCase();
      return status === "sell" || status === "published" || status === "reserved";
    }).length;
    const sold = allListings.filter((p) => String(p.status ?? "").toLowerCase() === "sold").length;
    const views = allListings.reduce((sum, p) => sum + Number(p.views_count ?? 0), 0);
    const favorites = allListings.reduce((sum, p) => sum + Number(p.favorites_count ?? 0), 0);
    const latest = [...allListings]
      .sort((a, b) => {
        const ta = a.created_at ? new Date(String(a.created_at)).getTime() : 0;
        const tb = b.created_at ? new Date(String(b.created_at)).getTime() : 0;
        return tb - ta;
      })
      .slice(0, 5);

    return { total, active, sold, views, favorites, latest };
  }, [allListings]);

  const handleLogout = () => {
    localStorage.removeItem("user");
    localStorage.removeItem("role");
    localStorage.removeItem("token");
    navigate("/login");
  };

  if (!user) {
    return <p>Loading dashboard...</p>;
  }

  return (
    <>
      <div className="user-dashboard-container">
        <div className="dashboard-left">
          <div className="dashboard">
            <aside className="links">
              <ul>
                <li>
                  <i className="fa-solid fa-gauge"></i>
                  <Link to="/user_dashboard">My Activity</Link>
                </li>
                <li>
                  <i className="fa-solid fa-shop"></i>
                  <Link to="/announcements">Marketplace</Link>
                </li>
                <li>
                  <i className="fa-solid fa-list"></i>
                  <Link to="/my_announcements">My Announcements</Link>
                </li>
                <li>
                  <i className="fa-solid fa-user"></i>
                  <Link to="/my_profile">Profile Settings</Link>
                </li>
                <li>
                  <i className="fa-solid fa-arrow-right-from-bracket"></i>
                  <button type="button" className="logout-btn" onClick={handleLogout}>
                    Logout
                  </button>
                </li>
              </ul>
            </aside>

            <main className="dashboard-main">
              <h2>Welcome, {(user.name as string) ?? (user.user_name as string) ?? "Guest"}</h2>
              <div className="stats-container">
                <div className="stat-card">
                  <p className="stat-number">{loadingListings ? "…" : stats.total}</p>
                  <p>Total listings</p>
                </div>
                <div className="stat-card">
                  <p className="stat-number">{loadingListings ? "…" : stats.active}</p>
                  <p>Active</p>
                </div>
                <div className="stat-card">
                  <p className="stat-number">{loadingListings ? "…" : stats.views}</p>
                  <p>Total views</p>
                </div>
                <div className="stat-card">
                  <p className="stat-number">{loadingListings ? "…" : stats.favorites}</p>
                  <p>Favorites</p>
                </div>
              </div>

              <div style={{ padding: "1.5rem 2rem" }}>
                <h3 style={{ margin: "0 0 0.75rem" }}>Recent activity</h3>
                {loadingListings ? (
                  <p>Loading activity...</p>
                ) : stats.latest.length > 0 ? (
                  <ul style={{ margin: 0, paddingLeft: "1.2rem" }}>
                    {stats.latest.map((p) => {
                      const slug = (p.slug as string | undefined) ?? "";
                      const title = (p.title as string | undefined) ?? "—";
                      const created = p.created_at ? new Date(String(p.created_at)).toLocaleDateString() : "—";
                      const status = (p.status as string | undefined) ?? "—";
                      return (
                        <li key={String(p.id)}>
                          <span style={{ fontWeight: 600 }}>{title}</span>{" "}
                          <span style={{ color: "#64748b" }}>({status}, {created})</span>{" "}
                          {slug ? (
                            <Link to={`/announcements/${slug}`} style={{ marginLeft: 6 }}>
                              View
                            </Link>
                          ) : null}
                        </li>
                      );
                    })}
                  </ul>
                ) : (
                  <p>
                    No activity yet. <Link to="/add_announcement">Post your first announcement</Link>.
                  </p>
                )}
              </div>
            </main>
          </div>
        </div>

        <div className="dashboard-right">
          <div className="new-listing">
            <h3>Your marketplace</h3>
            <p>
              Post items you want to sell. Buyers contact you directly by phone to arrange pickup or
              delivery.
            </p>
            <Link to="/add_announcement" className="cta-link">
              Add announcement
            </Link>
          </div>
        </div>
      </div>

      <div className="listing-history full-width">
        <div className="recent-toolbar">
          <h3>Recent announcements</h3>
          <label className="recent-filter-label">
            <span>Show</span>
            <select
              className="recent-filter-select"
              value={recentFilter}
              onChange={(e) => setRecentFilter(e.target.value as "all" | "sale")}
            >
              <option value="all">All listings</option>
              <option value="sale">For sale only</option>
            </select>
          </label>
        </div>

        <table>
          <thead>
            <tr>
              <th>Type</th>
              <th>Title</th>
              <th>Quantity / Specs</th>
              <th>Image</th>
              <th>Date</th>
              <th>Category</th>
              <th>Phone</th>
              <th>Status</th>
              <th>Region / Pickup</th>
            </tr>
          </thead>

          <tbody>
            {displayedRows.length > 0 ? (
              displayedRows.map((d) => {
                const rowId = d.id as number;
                const title = (d.title as string) ?? "—";
                const quantity = d.quantity ? `${d.quantity} ${d.quantity_unit || 'kg'}` : "—";
                const url = thumbUrl(d);
                const created = d.created_at ? new Date(String(d.created_at)).toLocaleDateString() : "—";
                const cat = (d.super_category as { name?: string } | undefined)?.name ?? "—";
                const status = (d.status as string) ?? "—";
                const pickup = (d.pickup_address as string) ?? (d.region as string) ?? "—";
                const phone = (d.contact_phone as string) ?? "—";

                return (
                  <tr key={rowId}>
                    <td>Sale</td>
                    <td>{title}</td>
                    <td>{quantity}</td>
                    <td>
                      {url ? (
                        <img
                          src={url}
                          alt=""
                          style={{
                            width: "50px",
                            borderRadius: "4px",
                            cursor: "pointer",
                          }}
                          onClick={() => {
                            setModalImage(url);
                            setModalOpen(true);
                          }}
                        />
                      ) : (
                        "—"
                      )}
                    </td>
                    <td>{created}</td>
                    <td>{cat}</td>
                    <td>{phone}</td>
                    <td>{status}</td>
                    <td>{pickup}</td>
                  </tr>
                );
              })
            ) : (
              <tr>
                <td colSpan={9}>Nothing to show for this filter yet.</td>
              </tr>
            )}
          </tbody>
        </table>
      </div>

      {modalOpen && modalImage ? (
        <div
          role="dialog"
          aria-modal="true"
          style={{
            position: "fixed",
            inset: 0,
            background: "rgba(0,0,0,0.75)",
            display: "flex",
            alignItems: "center",
            justifyContent: "center",
            zIndex: 1000,
            padding: "1rem",
          }}
          onClick={() => setModalOpen(false)}
        >
          <img
            src={modalImage}
            alt="Listing"
            style={{ maxWidth: "90vw", maxHeight: "90vh", borderRadius: 8 }}
            onClick={(e) => e.stopPropagation()}
          />
        </div>
      ) : null}
    </>
  );
}
