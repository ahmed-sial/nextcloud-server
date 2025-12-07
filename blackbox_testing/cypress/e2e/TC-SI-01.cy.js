describe("TC-SI-01", () => {
  it("tests TC-SI-01", () => {
    cy.viewport(1089, 825);
    cy.visit("http://localhost:8080/login");
    cy.get("#user").click();
    cy.get("#user").type("uchihas");
    cy.get("#password").click();
      cy.get("#password").type("22385427332abdulhaseeb");
      cy.get("fieldset > button svg").click();

    cy.location("href").should("eq", "http://localhost:8080/apps/dashboard/");
  });
});
