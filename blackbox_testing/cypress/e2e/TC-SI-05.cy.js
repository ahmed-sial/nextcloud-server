describe("TC-SI-05", () => {
  it("tests TC-SI-05", () => {
    cy.viewport(1089, 825);
    cy.visit("http://localhost:8080/login");
    cy.get("#user").click();
    cy.get("#user").type("nonexistent");
    cy.get("#password").click();
      cy.get("#password").type("anypass");
      cy.get("fieldset > button svg").click();

  });
});
